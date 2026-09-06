import React from 'react';
import { GymSubMenuScreen } from './GymSubMenuScreen';

const ITEMS = [
  { key: 'morning', icon: 'sunny-outline', label: 'Morning Workout' },
  { key: 'evening', icon: 'moon-outline', label: 'Evening Workout' },
  { key: 'challenge', icon: 'trophy-outline', label: 'Weekly Challenge' },
  { key: 'event', icon: 'calendar-outline', label: 'Gym Event' },
];

export default function ActivitiesScreen({ navigation }) {
  return (
    <GymSubMenuScreen
      title="Activities"
      subtitle="Aktivitas gym & challenge"
      items={ITEMS}
      onSelect={() => {}}
      onBack={() => navigation.goBack()}
    />
  );
}