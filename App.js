import React, { useState, useEffect } from 'react';
import { NavigationContainer, createNavigationContainerRef } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { onAuthStateChanged } from 'firebase/auth';
import { doc, getDoc } from 'firebase/firestore';
import { auth, db } from './src/config/firebase';
import { NotificationService } from './src/services/NotificationService';

const navigationRef = createNavigationContainerRef();

NotificationService.configurePresentation();

import LandingScreen from './src/screens/LandingScreen';
import LoadingScreen from './src/screens/LoadingScreen';
import LoginScreen from './src/screens/LoginScreen';
import RegisterScreen from './src/screens/RegisterScreen';
import AdminDashboard from './src/screens/admin/AdminDashboard';
import AdminAttendanceScreen from './src/screens/admin/AttendanceScreen';
import AdminClassAttendanceScanScreen from './src/screens/admin/ClassAttendanceScanScreen';
import ClassSchedulingScreen from './src/screens/admin/ClassSchedulingScreen';
import UserReservationHistoryScreen from './src/screens/admin/UserReservationHistoryScreen';
import RatingMeScreen from './src/screens/admin/RatingMeScreen';
import EventScreen from './src/screens/common/EventScreen';
import UserDashboard from './src/screens/user/UserDashboard';
import FieldReservationScreen from './src/screens/user/FieldReservationScreen';
import ClassScheduleScreen from './src/screens/user/ClassScheduleScreen';
import MyReservationHistoryScreen from './src/screens/user/MyReservationHistoryScreen';
import UserAttendanceScreen from './src/screens/user/AttendanceScreen';
import GscPackageScreen from './src/screens/user/GscPackageScreen';
import BuyPackageScreen from './src/screens/user/BuyPackageScreen';
import NotificationScreen from './src/screens/user/NotificationScreen';
import ETicketScreen from './src/screens/user/ETicketScreen';
import ProfileScreen from './src/screens/user/ProfileScreen';

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
      <Stack.Screen name="AdminDashboard" component={AdminDashboard} options={{ headerShown: false }} />
      <Stack.Screen name="Attendance" component={AdminAttendanceScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ClassAttendanceScan" component={AdminClassAttendanceScanScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ClassScheduling" component={ClassSchedulingScreen} options={{ headerShown: false }} />
      <Stack.Screen name="UserReservationHistory" component={UserReservationHistoryScreen} options={{ headerShown: false }} />
      <Stack.Screen name="RatingMe" component={RatingMeScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Events" component={EventScreen} options={{ headerShown: false }} />
    </Stack.Navigator>
  );
}

function UserStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen name="UserDashboard" component={UserDashboard} options={{ headerShown: false }} />
      <Stack.Screen name="Attendance" component={UserAttendanceScreen} options={{ headerShown: false }} />
      <Stack.Screen name="FieldReservation" component={FieldReservationScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ClassSchedule" component={ClassScheduleScreen} options={{ headerShown: false }} />
      <Stack.Screen name="MyReservationHistory" component={MyReservationHistoryScreen} options={{ headerShown: false }} />
      <Stack.Screen name="GscPackage" component={GscPackageScreen} options={{ headerShown: false }} />
      <Stack.Screen name="BuyPackage" component={BuyPackageScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Notifications" component={NotificationScreen} options={{ headerShown: false }} />
      <Stack.Screen name="ETicket" component={ETicketScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Profile" component={ProfileScreen} options={{ headerShown: false }} />
      <Stack.Screen name="Events" component={EventScreen} options={{ headerShown: false }} />
    </Stack.Navigator>
  );
}

export default function App() {
  const [user, setUser] = useState(null);
  const [role, setRole] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const unsubscribeAuth = onAuthStateChanged(auth, async (currentUser) => {
      if (currentUser) {
        setUser(currentUser);
        try {
          const userDoc = await getDoc(doc(db, 'users', currentUser.uid));
          if (userDoc.exists()) {
            setRole(userDoc.data().role);
            await NotificationService.registerForPushNotificationsAsync();
          }
        } catch (error) {
          console.error('Error fetching user role:', error);
        }
      } else {
        setUser(null);
        setRole(null);
      }
      setLoading(false);
    });

    const unsubscribeNotifications = NotificationService.addNotificationListeners(
      null,
      (response) => {
        NotificationService.handleNotificationNavigation(response, navigationRef);
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
