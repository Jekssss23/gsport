import * as WebBrowser from 'expo-web-browser';
import * as Crypto from 'expo-crypto';
import Constants, { ExecutionEnvironment } from 'expo-constants';
import { useState } from 'react';
import { AuthRequest, ResponseType, Prompt, makeRedirectUri } from 'expo-auth-session';
import { discovery } from 'expo-auth-session/providers/google';
import { GoogleAuthProvider, signInWithCredential } from 'firebase/auth';
import { doc, getDoc, setDoc } from 'firebase/firestore';
import { auth, db } from '../config/firebase';

WebBrowser.maybeCompleteAuthSession();

const WEB_CLIENT_ID = '253539109830-gnhd2rd14n7ourhiap23s8t1jer3ta8q.apps.googleusercontent.com';

const IS_EXPO_GO = Constants.executionEnvironment === ExecutionEnvironment.StoreClient;

let nativeGoogleConfigured = false;

const getNativeGoogleSignin = () => {
  const { GoogleSignin } = require('@react-native-google-signin/google-signin');
  return GoogleSignin;
};

const promptProxyGoogleAuth = async () => {
  const nonce = Crypto.randomUUID().replace(/-/g, '');
  const request = new AuthRequest({
    clientId: WEB_CLIENT_ID,
    redirectUri: `https://auth.expo.io/${Constants.expoConfig?.originalFullName || '@rbsoftware/GSC'}`,
    responseType: ResponseType.IdToken,
    scopes: ['openid', 'profile', 'email'],
    prompt: Prompt.SelectAccount,
    usePKCE: false,
    extraParams: { nonce },
  });

  await request.getAuthRequestConfigAsync();
  const authUrl = await request.makeAuthUrlAsync(discovery);
  const returnUrl = makeRedirectUri({ path: 'redirect' });
  const proxyBaseUrl = `https://auth.expo.io/${Constants.expoConfig?.originalFullName || '@rbsoftware/GSC'}`;
  const startUrl = `${proxyBaseUrl}/start?${new URLSearchParams({ authUrl, returnUrl })}`;

  const result = await WebBrowser.openAuthSessionAsync(startUrl, returnUrl);

  if (result.type !== 'success') {
    return { type: result.type };
  }

  return request.parseReturnUrl(result.url);
};

const promptNativeGoogleAuth = async () => {
  try {
    const GoogleSignin = getNativeGoogleSignin();
    if (!nativeGoogleConfigured) {
      GoogleSignin.configure({ webClientId: WEB_CLIENT_ID });
      nativeGoogleConfigured = true;
    }
    const response = await GoogleSignin.signIn();
    if (response.type !== 'success') {
      return { type: 'cancelled' };
    }
    return {
      type: 'success',
      params: { id_token: response.data.idToken },
    };
  } catch (error) {
    console.error('Native Google Sign-In Error:', error);
    return { type: 'error', error };
  }
};

const promptGoogleAuth = async () => {
  if (IS_EXPO_GO) {
    return promptProxyGoogleAuth();
  }
  return promptNativeGoogleAuth();
};

export const useGoogleAuthRequest = () => {
  const [request] = useState(true);
  const promptAsync = promptGoogleAuth;
  return { request, promptAsync };
};

export const handleGoogleSignIn = async (promptAsync) => {
  try {
    console.log('Starting Google Sign-In...');
    const result = await promptAsync();
    console.log('Google Sign-In result type:', result?.type);
    if (result?.type !== 'success') {
      console.log('Full result:', JSON.stringify(result));
    }

    if (result?.type !== 'success') {
      throw new Error(`Google Sign-In was cancelled or failed: ${result?.type}`);
    }

    const { id_token } = result.params;
    console.log('Got id_token:', !!id_token);
    const credential = GoogleAuthProvider.credential(id_token);
    const userCredential = await signInWithCredential(auth, credential);
    const user = userCredential.user;

    const userDoc = await getDoc(doc(db, 'users', user.uid));

    if (!userDoc.exists()) {
      const userData = {
        email: user.email,
        name: user.displayName || user.email.split('@')[0],
        phoneNumber: user.phoneNumber || '',
        photoURL: user.photoURL || '',
        role: 'user',
        provider: 'google',
        createdAt: new Date().toISOString(),
      };

      await setDoc(doc(db, 'users', user.uid), userData);
    }

    return { user, isNewUser: !userDoc.exists() };
  } catch (error) {
    console.error('Google Sign-In Error:', error);
    throw error;
  }
};

export const handleGoogleSignOut = async () => {
  if (IS_EXPO_GO) {
    return;
  }
  try {
    const GoogleSignin = getNativeGoogleSignin();
    await GoogleSignin.signOut();
  } catch (error) {
    console.warn('Google Sign-Out Error:', error);
  }
};
