import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons } from '@expo/vector-icons';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { theme } from '../styles/theme';
import { CircleMenu } from '../../assets/ui/navigation';

import HomeScreen from '../screens/user/HomeScreen';
import FieldReservationScreen from '../screens/user/FieldReservationScreen';
import MyReservationHistoryScreen from '../screens/user/MyReservationHistoryScreen';
import ClassScheduleScreen from '../screens/user/ClassScheduleScreen';
import GscPackageScreen from '../screens/user/GscPackageScreen';
import ProfileScreen from '../screens/user/ProfileScreen';
import GymScreen from '../screens/user/GymScreen';

const Tab = createBottomTabNavigator();

const DOCK_MENU = [
  { key: 'FieldBooking', label: 'Sports', icon: 'football' },
  { key: 'Gym', label: 'Gym', icon: 'barbell' },
  { key: 'History', label: 'History', icon: 'time' },
  { key: 'Schedules', label: 'Schedule', icon: 'calendar' },
  { key: 'GscPackages', label: 'Package', icon: 'cube' },
];

function DockButton({ icon, label, active, onPress }) {
  return (
    <TouchableOpacity
      style={styles.dockSide}
      activeOpacity={0.7}
      onPress={onPress}
    >
      <View style={[styles.dockSideIcon, active && styles.dockSideIconActive]}>
        <Ionicons name={icon} size={22} color={active ? theme.colors.primary : theme.colors.text} />
      </View>
      {label ? (
        <Text style={[styles.dockSideLabel, active && styles.dockSideLabelActive]}>{label}</Text>
      ) : null}
    </TouchableOpacity>
  );
}

function CustomDock({ state, navigation }) {
  const insets = useSafeAreaInsets();
  const current = state.routes[state.index]?.name;
  const [menuOpen, setMenuOpen] = useState(false);

  const go = (name) => {
    setMenuOpen(false);
    const event = navigation.emit({ type: 'tabPress', target: undefined, canPreventDefault: true });
    if (!event.defaultPrevented) {
      navigation.navigate(name);
    }
  };

  const handleSelect = (key) => {
    setMenuOpen(false);
    go(key);
  };

  return (
    <View style={[styles.dockWrap, { paddingBottom: (insets.bottom || 8) }]} pointerEvents="box-none">
      <View style={styles.dock} pointerEvents="box-none">
        {!menuOpen && (
          <DockButton
            icon={current === 'Home' ? 'home' : 'home-outline'}
            label="Home"
            active={current === 'Home'}
            onPress={() => go('Home')}
          />
        )}
        <CircleMenu
          items={DOCK_MENU}
          activeKey={current}
          open={menuOpen}
          onOpenChange={setMenuOpen}
          onSelect={handleSelect}
        />
        {!menuOpen && (
          <DockButton
            icon={current === 'Profile' ? 'person' : 'person-outline'}
            label="Profile"
            active={current === 'Profile'}
            onPress={() => go('Profile')}
          />
        )}
      </View>
    </View>
  );
}

export default function UserBottomTabs() {
  return (
    <Tab.Navigator
      tabBar={(props) => <CustomDock {...props} />}
      screenOptions={{ headerShown: false }}
      initialRouteName="Home"
    >
      <Tab.Screen name="Home" component={HomeScreen} />
      <Tab.Screen
        name="FieldBooking"
        component={FieldReservationScreen}
        options={{ unmountOnBlur: true }}
      />
      <Tab.Screen
        name="Gym"
        component={GymScreen}
        options={{ unmountOnBlur: true }}
      />
      <Tab.Screen
        name="History"
        component={MyReservationHistoryScreen}
        options={{ unmountOnBlur: true }}
      />
      <Tab.Screen
        name="Schedules"
        component={ClassScheduleScreen}
        options={{ unmountOnBlur: true }}
      />
      <Tab.Screen
        name="GscPackages"
        component={GscPackageScreen}
        options={{ unmountOnBlur: true }}
      />
      <Tab.Screen name="Profile" component={ProfileScreen} />
    </Tab.Navigator>
  );
}

const styles = StyleSheet.create({
  dockWrap: {
    position: 'absolute',
    left: 0,
    right: 0,
    bottom: 0,
    alignItems: 'center',
  },
  dock: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    width: 236,
    backgroundColor: 'rgba(24, 24, 24, 0.9)',
    borderRadius: 30,
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.12)',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.5,
    shadowRadius: 14,
    elevation: 18,
  },
  dockSide: {
    alignItems: 'center',
    justifyContent: 'center',
    width: 74,
    gap: 3,
  },
  dockSideIcon: {
    width: 44,
    height: 34,
    borderRadius: 17,
    justifyContent: 'center',
    alignItems: 'center',
  },
  dockSideIconActive: {
    backgroundColor: 'rgba(230, 0, 0, 0.14)',
  },
  dockSideLabel: {
    color: theme.colors.textSecondary,
    fontSize: 10,
    fontWeight: '600',
    letterSpacing: 0.3,
  },
  dockSideLabelActive: {
    color: theme.colors.primary,
    fontWeight: '800',
  },
});
