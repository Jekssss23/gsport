import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Alert, Dimensions } from 'react-native';
import { signOut } from 'firebase/auth';
import { doc, getDoc } from 'firebase/firestore';
import { auth, db } from '../../config/firebase.js';
import { theme } from '../../styles/theme';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

const { width } = Dimensions.get('window');

export default function UserDashboard({ navigation }) {
  const [userName, setUserName] = useState('');

  useEffect(() => {
    const fetchUserData = async () => {
      if (auth.currentUser) {
        try {
          const userDoc = await getDoc(doc(db, 'users', auth.currentUser.uid));
          if (userDoc.exists()) {
            setUserName(userDoc.data().name);
          }
        } catch (error) {
          console.error("Error fetching user data:", error);
        }
      }
    };
    fetchUserData();
  }, []);

  const handleLogout = () => {
    Alert.alert(
      "Logout",
      "Are you sure you want to logout?",
      [
        { text: "Cancel", style: "cancel" },
        { 
          text: "Logout", 
          onPress: () => signOut(auth).catch(error => console.error('Error signing out: ', error)),
          style: 'destructive'
        }
      ]
    );
  };

  const DashboardCard = ({ title, icon, route, gradientColors }) => (
    <TouchableOpacity 
      style={styles.cardContainer}
      onPress={() => {
        if (route === 'FieldReservation') {
          navigation.navigate(route, { user: auth.currentUser });
        } else {
          navigation.navigate(route);
        }
      }}
      activeOpacity={0.9}
    >
      <LinearGradient
        colors={gradientColors}
        style={styles.card}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      >
        <View style={styles.iconContainer}>
          <Ionicons name={icon} size={32} color="white" />
        </View>
        <Text style={styles.cardTitle}>{title}</Text>
        <Ionicons name="arrow-forward-circle" size={24} color="rgba(255,255,255,0.8)" style={styles.arrowIcon} />
      </LinearGradient>
    </TouchableOpacity>
  );

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <View>
          <Text style={styles.welcomeText}>Welcome back,</Text>
          <Text style={styles.userName}>{userName || 'User'}</Text>
        </View>
        <TouchableOpacity onPress={handleLogout} style={styles.logoutButton}>
          <Ionicons name="log-out-outline" size={24} color={theme.colors.primary} />
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        <Text style={styles.sectionTitle}>Dashboard</Text>
        
        <DashboardCard 
          title="Field Reservation" 
          icon="calendar" 
          route="FieldReservation"
          gradientColors={['#FF0000', '#CC0000']} 
        />

        <DashboardCard 
          title="My Reservations" 
          icon="time" 
          route="MyReservationHistory"
          gradientColors={['#333333', '#000000']} 
        />

        <DashboardCard 
          title="Class Schedule" 
          icon="list" 
          route="ClassSchedule"
          gradientColors={['#1a1a1a', '#000000']} 
        />

        <View style={styles.promoCard}>
          <LinearGradient
            colors={['#1a1a1a', '#000000']}
            style={styles.promoContent}
          >
            <Text style={styles.promoTitle}>Welcome to G Sports Center</Text>
            <Text style={styles.promoText}>Book your favorite sports facilities now!</Text>
          </LinearGradient>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 60,
    paddingBottom: 20,
    backgroundColor: theme.colors.cardBackground,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,255,255,0.1)',
  },
  welcomeText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
  userName: {
    color: theme.colors.text,
    fontSize: 20,
    fontWeight: 'bold',
  },
  logoutButton: {
    padding: 10,
    backgroundColor: 'rgba(255,0,0,0.1)',
    borderRadius: 50,
  },
  content: {
    padding: 20,
  },
  sectionTitle: {
    color: theme.colors.text,
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 20,
    marginLeft: 5,
  },
  cardContainer: {
    marginBottom: 20,
    borderRadius: 15,
    shadowColor: "#000",
    shadowOffset: {
      width: 0,
      height: 4,
    },
    shadowOpacity: 0.30,
    shadowRadius: 4.65,
    elevation: 8,
  },
  card: {
    padding: 20,
    borderRadius: 15,
    flexDirection: 'row',
    alignItems: 'center',
    height: 100,
  },
  iconContainer: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
  },
  cardTitle: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
    flex: 1,
  },
  arrowIcon: {
    opacity: 0.8,
  },
  promoCard: {
    marginTop: 10,
    borderRadius: 15,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.1)',
  },
  promoContent: {
    padding: 20,
    alignItems: 'center',
  },
  promoTitle: {
    color: theme.colors.primary,
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  promoText: {
    color: theme.colors.textSecondary,
    fontSize: 14,
  },
});
