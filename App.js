import React, { useState, useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { onAuthStateChanged } from 'firebase/auth';
import { doc, getDoc } from 'firebase/firestore';
import { ActivityIndicator, View } from 'react-native';
import { auth, db } from './src/config/firebase';

// Screens
import LandingScreen from './src/screens/LandingScreen';
import LoadingScreen from './src/screens/LoadingScreen';
import LoginScreen from './src/screens/LoginScreen';
import RegisterScreen from './src/screens/RegisterScreen';

// Admin Screens
import AdminDashboard from './src/screens/admin/AdminDashboard';
import AdminAttendanceScreen from './src/screens/admin/AttendanceScreen';
import ClassSchedulingScreen from './src/screens/admin/ClassSchedulingScreen';
import UserReservationHistoryScreen from './src/screens/admin/UserReservationHistoryScreen';
import RatingMeScreen from './src/screens/admin/RatingMeScreen';

// User Screens
import UserDashboard from './src/screens/user/UserDashboard';
import FieldReservationScreen from './src/screens/user/FieldReservationScreen';
import ClassScheduleScreen from './src/screens/user/ClassScheduleScreen';
import MyReservationHistoryScreen from './src/screens/user/MyReservationHistoryScreen';
import UserAttendanceScreen from './src/screens/user/AttendanceScreen';

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
      <Stack.Screen name="ClassScheduling" component={ClassSchedulingScreen} options={{ title: 'Schedule Class' }} />
      <Stack.Screen name="UserReservationHistory" component={UserReservationHistoryScreen} options={{ title: 'User History' }} />
      <Stack.Screen name="RatingMe" component={RatingMeScreen} options={{ title: 'Rating Me' }} />
    </Stack.Navigator>
  );
}

function UserStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen name="UserDashboard" component={UserDashboard} options={{ title: 'User Dashboard' }} />
      <Stack.Screen name="Attendance" component={UserAttendanceScreen} options={{ headerShown: false }} />
      <Stack.Screen name="FieldReservation" component={FieldReservationScreen} options={{ title: 'Reserve Field' }} />
      <Stack.Screen name="ClassSchedule" component={ClassScheduleScreen} options={{ title: 'Class Schedule' }} />
      <Stack.Screen name="MyReservationHistory" component={MyReservationHistoryScreen} options={{ title: 'My History' }} />
    </Stack.Navigator>
  );
}

export default function App() {
  const [user, setUser] = useState(null);
  const [role, setRole] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const unsubscribe = onAuthStateChanged(auth, async (user) => {
      if (user) {
        setUser(user);
        try {
          const userDoc = await getDoc(doc(db, 'users', user.uid));
          if (userDoc.exists()) {
            setRole(userDoc.data().role);
          } else {
            // Fallback if doc doesn't exist yet (e.g. slight delay in registration)
            // In a real app, you might want to listen to the document or retry.
            // For now, we'll leave role null and let it try to re-render or just stay on loading?
            // Actually, if we set loading false and role is null, it will go to AuthStack because logic below:
            // user && role === 'admin' -> Admin
            // user && role === 'user' -> User
            // else -> Auth
            // So if logged in but no role, it shows Auth (Login). This is safer than crashing.
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

    return unsubscribe;
  }, []);

  if (loading) {
    return <LoadingScreen />;
  }

  return (
    <NavigationContainer>
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
