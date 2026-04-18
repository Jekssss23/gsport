import React, { useState, useEffect, useRef } from 'react';
import { NavigationContainer, createNavigationContainerRef } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { onAuthStateChanged } from 'firebase/auth';
import { doc, getDoc } from 'firebase/firestore';
import { ActivityIndicator, View, Alert } from 'react-native';
import * as Notifications from 'expo-notifications';
import { auth, db } from './src/config/firebase';
import { NotificationService } from './src/services/NotificationService';

// Create navigation ref
const navigationRef = createNavigationContainerRef();

// Configure notifications handler
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
  }),
});

// Screens
import LandingScreen from './src/screens/LandingScreen';
import LoadingScreen from './src/screens/LoadingScreen';
import LoginScreen from './src/screens/LoginScreen';
import RegisterScreen from './src/screens/RegisterScreen';

// Admin Screens
import AdminDashboard from './src/screens/admin/AdminDashboard';
import AdminAttendanceScreen from './src/screens/admin/AttendanceScreen';
import AdminClassAttendanceScanScreen from './src/screens/admin/ClassAttendanceScanScreen';
import ClassSchedulingScreen from './src/screens/admin/ClassSchedulingScreen';
import UserReservationHistoryScreen from './src/screens/admin/UserReservationHistoryScreen';
import RatingMeScreen from './src/screens/admin/RatingMeScreen';

// User Screens
import UserDashboard from './src/screens/user/UserDashboard';
import FieldReservationScreen from './src/screens/user/FieldReservationScreen';
import ClassScheduleScreen from './src/screens/user/ClassScheduleScreen';
import MyReservationHistoryScreen from './src/screens/user/MyReservationHistoryScreen';
import UserAttendanceScreen from './src/screens/user/AttendanceScreen';
import GscPackageScreen from './src/screens/user/GscPackageScreen';
import BuyPackageScreen from './src/screens/user/BuyPackageScreen';
import NotificationScreen from './src/screens/user/NotificationScreen';

const Stack = createNativeStackNavigator();

function AuthStack() {
  return (
    <Stack.Navigator initialRouteName="Landing">
      <Stack.Screen name="Landing" component={LandingScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Login" component={LoginScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Register" component={RegisterScreen} options={{ headerShown: false }} />
    </Stack.Navigator>
  );
}

function AdminStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen name="AdminDashboard" component={AdminDashboard} options={{ title: 'Admin Dashboard' }} />
      <Stack.Screen name="Attendance" component={AdminAttendanceScreen} options={{ title: 'Attendance' }} />
      <Stack.Screen name="ClassAttendanceScan" component={AdminClassAttendanceScanScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ClassScheduling" component={ClassSchedulingScreen} options={{ title: 'Schedule Class' }} />
      <Stack.Screen name="UserReservationHistory" component={UserReservationHistoryScreen} options={{ title: 'User History' }} />
      <Stack.Screen name="RatingMe" component={RatingMeScreen} options={{ title: 'Rating Me' }} />
    </Stack.Navigator>
  );
}

function UserStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen name="UserDashboard" component={UserDashboard} options={{ headerShown: false }} />
      <Stack.Screen name="Attendance" component={UserAttendanceScreen} options={{ headerShown: false }} />
      <Stack.Screen name="FieldReservation" component={FieldReservationScreen} options={{ title: 'Reserve Field' }} />
      <Stack.Screen name="ClassSchedule" component={ClassScheduleScreen} options={{ title: 'Class Schedule' }} />
      <Stack.Screen name="MyReservationHistory" component={MyReservationHistoryScreen} options={{ title: 'My History' }} />
      <Stack.Screen name="GscPackage" component={GscPackageScreen} options={{ title: 'Paket GSC' }} />
      <Stack.Screen name="BuyPackage" component={BuyPackageScreen} options={{ title: 'Beli Paket GSC' }} />
      <Stack.Screen name="Notifications" component={NotificationScreen} options={{ headerShown: false }} />
    </Stack.Navigator>
  );
}

export default function App() {
  const [user, setUser] = useState(null);
  const [role, setRole] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const unsubscribeAuth = onAuthStateChanged(auth, async (user) => {
      if (user) {
        setUser(user);
        try {
          const userDoc = await getDoc(doc(db, 'users', user.uid));
          if (userDoc.exists()) {
            setRole(userDoc.data().role);
            // Register for push notifications
            NotificationService.registerForPushNotificationsAsync();
          }
        } catch (error) {
          console.error("Error fetching user role:", error);
        }
      } else {
        setUser(null);
        setRole(null);
      }
      setLoading(false);
    });

    // Set up notification listeners
    const unsubscribeNotifications = NotificationService.addNotificationListeners(
      (notification) => {
        // Handle foreground notification
        console.log('Notification received in foreground:', notification);
      },
      (response) => {
        // Handle notification tap
        console.log('Notification tapped:', response);
        const data = response.notification.request.content.data;
        
        if (data && data.type === 'rating_prompt') {
          // Navigate to Notifications screen
          if (navigationRef.isReady()) {
            navigationRef.navigate('Notifications');
          }
        }
      }
    );

    return () => {
      unsubscribeAuth();
      unsubscribeNotifications();
    };
  }, []);

  if (loading) {
    return <LoadingScreen />;
  }

  return (
    <NavigationContainer ref={navigationRef}>
      {user && role === 'admin' ? (
        <AdminStack />
      ) : user && role === 'user' ? (
        <UserStack />
      ) : (
        <AuthStack />
      )}
    </NavigationContainer>
  );
}
