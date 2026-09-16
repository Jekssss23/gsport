import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import Constants from 'expo-constants';
import { Platform, AppState } from 'react-native';
import { db, auth } from '../config/firebase';
import { doc, updateDoc } from 'firebase/firestore';

const CHANNELS = {
  default: {
    id: 'default',
    name: 'Umum',
    importance: Notifications.AndroidImportance.MAX,
    vibrationPattern: [0, 250, 250, 250],
    lightColor: '#E31E24',
    sound: 'default',
  },
  events: {
    id: 'events',
    name: 'Event G Sports',
    importance: Notifications.AndroidImportance.HIGH,
    vibrationPattern: [0, 200, 100, 200],
    lightColor: '#E31E24',
    sound: 'default',
  },
  reservations: {
    id: 'reservations',
    name: 'Reservasi',
    importance: Notifications.AndroidImportance.MAX,
    vibrationPattern: [0, 300, 150, 300],
    lightColor: '#E31E24',
    sound: 'default',
  },
};

function resolveChannelId(data = {}) {
  const type = data?.type || '';
  if (type === 'event') return CHANNELS.events.id;
  if (type === 'rating_prompt' || type === 'reservation_confirmed' || type === 'reservation') {
    return CHANNELS.reservations.id;
  }
  return CHANNELS.default.id;
}

export class NotificationService {
  static async setupAndroidChannels() {
    if (Platform.OS !== 'android') return;

    for (const ch of Object.values(CHANNELS)) {
      await Notifications.setNotificationChannelAsync(ch.id, {
        name: ch.name,
        importance: ch.importance,
        vibrationPattern: ch.vibrationPattern,
        lightColor: ch.lightColor,
        sound: ch.sound,
        enableVibrate: true,
        showBadge: true,
      });
    }
  }

  static configurePresentation() {
    Notifications.setNotificationHandler({
      handleNotification: async (notification) => {
        const data = notification.request.content.data || {};
        return {
          shouldShowAlert: true,
          shouldShowBanner: true,
          shouldShowList: true,
          shouldPlaySound: true,
          shouldSetBadge: true,
          priority: Notifications.AndroidNotificationPriority.MAX,
          channelId: resolveChannelId(data),
        };
      },
    });
  }

  static async registerForPushNotificationsAsync() {
    await this.setupAndroidChannels();

    let token;

    if (Platform.OS === 'android') {
      await Notifications.setNotificationChannelAsync(CHANNELS.default.id, CHANNELS.default);
    }

    if (!Device.isDevice) {
      console.log('Push notifications require a physical device');
      return null;
    }

    const isExpoGo = Constants.appOwnership === 'expo';
    if (isExpoGo) {
      console.warn('Remote push tidak didukung di Expo Go. Gunakan development build / APK release.');
      return null;
    }

    const { status: existingStatus } = await Notifications.getPermissionsAsync();
    let finalStatus = existingStatus;
    if (existingStatus !== 'granted') {
      const { status } = await Notifications.requestPermissionsAsync({
        ios: {
          allowAlert: true,
          allowBadge: true,
          allowSound: true,
        },
      });
      finalStatus = status;
    }

    if (finalStatus !== 'granted') {
      console.log('Izin notifikasi ditolak');
      return null;
    }

    token = (
      await Notifications.getExpoPushTokenAsync({
        projectId: Constants.expoConfig?.extra?.eas?.projectId || '269e6350-dc6c-4096-ad18-a81585aa5135',
      })
    ).data;

    if (auth.currentUser && token) {
      await updateDoc(doc(db, 'users', auth.currentUser.uid), {
        pushToken: token,
        expoPushToken: token,
        lastTokenUpdate: new Date().toISOString(),
      });
    }

    return token;
  }

  /** Banner saat app terbuka (foreground) — seperti notif WA */
  static async presentForegroundBanner(notification) {
    if (AppState.currentState !== 'active') return;

    const content = notification?.request?.content;
    if (!content?.title) return;

    const data = content.data || {};
    await Notifications.scheduleNotificationAsync({
      content: {
        title: content.title,
        body: content.body || '',
        data,
        sound: 'default',
        priority: Notifications.AndroidNotificationPriority.MAX,
        channelId: resolveChannelId(data),
      },
      trigger: null,
    });
  }

  static async sendLocalNotification(title, body, data = {}) {
    await Notifications.scheduleNotificationAsync({
      content: {
        title,
        body,
        data,
        sound: 'default',
        priority: Notifications.AndroidNotificationPriority.MAX,
        channelId: resolveChannelId(data),
      },
      trigger: null,
    });
  }

  static handleNotificationNavigation(response, navigationRef) {
    if (!navigationRef?.isReady?.()) return;

    const data = response?.notification?.request?.content?.data || {};

    if (data.type === 'event') {
      navigationRef.navigate('Events');
      return;
    }

    if (data.type === 'rating_prompt' || data.type === 'reservation_confirmed') {
      navigationRef.navigate('Notifications');
      return;
    }
  }

  static addNotificationListeners(onNotificationReceived, onNotificationResponse) {
    const notificationListener = Notifications.addNotificationReceivedListener((notification) => {
      this.presentForegroundBanner(notification).catch(() => {});
      if (onNotificationReceived) onNotificationReceived(notification);
    });

    const responseListener = Notifications.addNotificationResponseReceivedListener((response) => {
      if (onNotificationResponse) onNotificationResponse(response);
    });

    return () => {
      notificationListener?.remove?.();
      responseListener?.remove?.();
    };
  }
}
