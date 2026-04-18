import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import Constants from 'expo-constants';
import { Platform } from 'react-native';
import { db, auth } from '../config/firebase';
import { doc, updateDoc } from 'firebase/firestore';

export class NotificationService {
  static async registerForPushNotificationsAsync() {
    let token;

    if (Platform.OS === 'android') {
      await Notifications.setNotificationChannelAsync('default', {
        name: 'default',
        importance: Notifications.AndroidImportance.MAX,
        vibrationPattern: [0, 250, 250, 250],
        lightColor: '#FF231F7C',
      });
    }

    if (Device.isDevice) {
      // Check if running in Expo Go
      const isExpoGo = Constants.appOwnership === 'expo';
      if (isExpoGo) {
        console.warn('Remote push notifications are not supported in Expo Go (SDK 53+). Please use a development build.');
        return null;
      }

      const { status: existingStatus } = await Notifications.getPermissionsAsync();
      let finalStatus = existingStatus;
      if (existingStatus !== 'granted') {
        const { status } = await Notifications.requestPermissionsAsync();
        finalStatus = status;
      }
      if (finalStatus !== 'granted') {
        console.log('Failed to get push token for push notification!');
        return;
      }
      token = (await Notifications.getExpoPushTokenAsync({
        projectId: 'your-project-id', // Replace with your Expo project ID
      })).data;
      
      console.log('Push Token:', token);

      if (auth.currentUser) {
        await updateDoc(doc(db, 'users', auth.currentUser.uid), {
          pushToken: token,
          lastTokenUpdate: new Date().toISOString()
        });
      }
    } else {
      console.log('Must use physical device for Push Notifications');
    }

    return token;
  }

  static async sendLocalNotification(title, body, data = {}) {
    await Notifications.scheduleNotificationAsync({
      content: {
        title,
        body,
        data,
      },
      trigger: null,
    });
  }

  static addNotificationListeners(onNotificationReceived, onNotificationResponse) {
    const notificationListener = Notifications.addNotificationReceivedListener(notification => {
      if (onNotificationReceived) onNotificationReceived(notification);
    });

    const responseListener = Notifications.addNotificationResponseReceivedListener(response => {
      if (onNotificationResponse) onNotificationResponse(response);
    });

    return () => {
      if (notificationListener && notificationListener.remove) {
        notificationListener.remove();
      }
      if (responseListener && responseListener.remove) {
        responseListener.remove();
      }
    };
  }
}
