import { Platform } from 'react-native';
import Constants from 'expo-constants';

/**
 * Base URL for CodeIgniter API (XAMPP).
 *
 * Priority:
 * 1) EXPO_PUBLIC_API_BASE_URL — full URL, e.g. http://192.168.1.10/g_sports_center_admin_robby/api
 * 2) Host dari Metro/Expo (IP yang sama dipakai HP untuk load bundle) — paling cocok untuk device fisik
 * 3) Android emulator → 10.0.2.2 (alias ke host Windows)
 * 4) iOS simulator → 127.0.0.1
 * 5) Fallback terakhir (ganti di .env jika perlu)
 */
const PROJECT_API_PATH = '/g_sports_center_admin_robby/api';

function stripTrailingSlash(url) {
  return url.replace(/\/+$/, '');
}

function resolveApiBaseUrl() {
  const envUrl = process.env.EXPO_PUBLIC_API_BASE_URL;
  if (envUrl && String(envUrl).trim()) {
    return stripTrailingSlash(String(envUrl).trim());
  }

  const fromExpo =
    Constants.expoConfig?.hostUri ||
    Constants.expoGoConfig?.debuggerHost ||
    Constants.manifest2?.extra?.expoGo?.debuggerHost ||
    Constants.manifest?.debuggerHost ||
    Constants.manifest?.hostUri;

  let host = null;
  if (fromExpo) {
    host = String(fromExpo).split(':')[0];
  }

  if (!host || host === 'localhost' || host === '127.0.0.1') {
    if (Platform.OS === 'android') {
      // Emulator: host machine. Device fisik: host dari Expo biasanya sudah terisi di atas.
      host = '10.0.2.2';
    } else if (Platform.OS === 'ios') {
      host = '127.0.0.1';
    } else {
      host = '192.168.56.1';
    }
  }
\
  const protocol = 'http';
  return `${protocol}://${host}${PROJECT_API_PATH}`;
}

export const API_BASE_URL = resolveApiBaseUrl();
