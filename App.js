import React, { useState, useEffect, useCallback } from 'react';
import { NavigationContainer, createNavigationContainerRef } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { onAuthStateChanged } from 'firebase/auth';
import { doc, getDoc, onSnapshot } from 'firebase/firestore';
import { auth, db } from './src/config/firebase';
import { NotificationService } from './src/services/NotificationService';
import { QueryProvider } from './src/config/queryClient';
import { Asset } from 'expo-asset';
import { useFonts } from 'expo-font';

const navigationRef = createNavigationContainerRef();

NotificationService.configurePresentation();

import SplashScreen from './src/screens/SplashScreen';
import LoginScreen from './src/screens/LoginScreen';
import RegisterScreen from './src/screens/RegisterScreen';
import AdminDashboard from './src/screens/admin/AdminDashboard';
import AdminAttendanceScreen from './src/screens/admin/AttendanceScreen';
import AdminClassAttendanceScanScreen from './src/screens/admin/ClassAttendanceScanScreen';
import ClassSchedulingScreen from './src/screens/admin/ClassSchedulingScreen';
import UserReservationHistoryScreen from './src/screens/admin/UserReservationHistoryScreen';
import RatingMeScreen from './src/screens/admin/RatingMeScreen';
import GscPackageManageScreen from './src/screens/admin/GscPackageManageScreen';
import EventScreen from './src/screens/common/EventScreen';
import EventDetailScreen from './src/screens/common/EventDetailScreen';
import UserBottomTabs from './src/navigation/UserBottomTabs';
import UserAttendanceScreen from './src/screens/user/AttendanceScreen';
import BuyPackageScreen from './src/screens/user/BuyPackageScreen';
import NotificationScreen from './src/screens/user/NotificationScreen';
import ETicketScreen from './src/screens/user/ETicketScreen';
import ClassAttendanceScanScreen from './src/screens/user/ClassAttendanceScanScreen';

const SPLASH_IMAGES = [
  require('./assets/images/pickle.jpg'),
  require('./assets/images/gym2.jpg'),
  require('./assets/images/swim.jpg'),
  require('./assets/images/muaythai.jpg'),
  require('./assets/LOGO/LOGO GSPORT (Full PUTIH).png'),
];

const preloadSplashAssets = async () => {
  try {
    await Asset.loadAsync(SPLASH_IMAGES);
  } catch (error) {
    console.warn('Splash asset preload failed:', error);
  }
};

const Stack = createNativeStackNavigator();

function AuthStack() {
  return (
    <Stack.Navigator initialRouteName="Splash">
      <Stack.Screen name="Splash" component={SplashScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Login" component={LoginScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Register" component={RegisterScreen} options={{ headerShown: false }} />
    </Stack.Navigator>
  );
}

function AdminStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen name="AdminDashboard" component={AdminDashboard} options={{ headerShown: false }} />
      <Stack.Screen name="Attendance" component={AdminAttendanceScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ClassAttendanceScan" component={AdminClassAttendanceScanScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ClassScheduling" component={ClassSchedulingScreen} options={{ headerShown: false }} />
      <Stack.Screen name="UserReservationHistory" component={UserReservationHistoryScreen} options={{ headerShown: false }} />
      <Stack.Screen name="RatingMe" component={RatingMeScreen} options={{ headerShown: false }} />
      <Stack.Screen name="GscPackageManage" component={GscPackageManageScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Events" component={EventScreen} options={{ headerShown: false }} />
    </Stack.Navigator>
  );
}

function UserStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen name="UserMain" component={UserBottomTabs} options={{ headerShown: false }} />
      <Stack.Screen name="Attendance" component={UserAttendanceScreen} options={{ headerShown: false }} />
      <Stack.Screen name="BuyPackage" component={BuyPackageScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Notifications" component={NotificationScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ETicket" component={ETicketScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Events" component={EventScreen} options={{ headerShown: false }} />
      <Stack.Screen name="EventDetail" component={EventDetailScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ClassAttendanceScan" component={ClassAttendanceScanScreen} options={{ headerShown: false }} />
    </Stack.Navigator>
  );
}

export default function App() {
  const [user, setUser] = useState(null);
  const [role, setRole] = useState(null);
  const [loading, setLoading] = useState(true);
  const [fontsLoaded] = useFonts({
    'Humane-Regular': require('./assets/FONT/humane/Humane-Regular-BF6a48d3b8cf291.ttf'),
    'Humane-Medium': require('./assets/FONT/humane/Humane-Medium-BF6a48d3b8cf2be.ttf'),
  });

  useEffect(() => {
    let mounted = true;

    const initApp = async () => {
      await preloadSplashAssets();
      
      if (!mounted) return;

      let unsubscribePendingUserDoc = null;

      const unsubscribeAuth = onAuthStateChanged(auth, async (currentUser) => {
        if (currentUser) {
          setUser(currentUser);
          try {
            const userDoc = await getDoc(doc(db, 'users', currentUser.uid));
            if (userDoc.exists()) {
              setRole(userDoc.data().role);
              await NotificationService.registerForPushNotificationsAsync();
            } else {
              // The user document can be created right after auth sign-in (e.g. Google sign-in),
              // so wait for it to appear before resolving the role.
              unsubscribePendingUserDoc = onSnapshot(doc(db, 'users', currentUser.uid), (snap) => {
                if (snap.exists()) {
                  setRole(snap.data().role);
                  unsubscribePendingUserDoc?.();
                  unsubscribePendingUserDoc = null;
                }
              });
            }
          } catch (error) {
            console.error('Error fetching user role:', error);
          }
        } else {
          setUser(null);
          setRole(null);
          unsubscribePendingUserDoc?.();
          unsubscribePendingUserDoc = null;
        }
        if (mounted) setLoading(false);
      });

      const unsubscribeNotifications = NotificationService.addNotificationListeners(
        null,
        (response) => {
          NotificationService.handleNotificationNavigation(response, navigationRef);
        }
      );

      return () => {
        unsubscribeAuth();
        unsubscribePendingUserDoc?.();
        unsubscribeNotifications();
      };
    };

    const cleanup = initApp();
    return () => {
      mounted = false;
      cleanup?.then?.(fn => fn?.());
    };
  }, []);

  if (loading || !fontsLoaded) {
    return null;
  }

  return (
    <QueryProvider>
      <NavigationContainer ref={navigationRef}>
        {user && role === 'admin' ? (
          <AdminStack />
        ) : user && role === 'user' ? (
          <UserStack />
        ) : (
          <AuthStack />
        )}
      </NavigationContainer>
    </QueryProvider>
  );
}
