import React from 'react';
import { GymSubMenuScreen } from './GymSubMenuScreen';

const ITEMS = [
  { key: 'trainer1', icon: 'person-outline', label: 'Rendy — Strength Coach' },
  { key: 'trainer2', icon: 'person-outline', label: 'Ayu — Cardio Coach' },
  { key: 'trainer3', icon: 'person-outline', label: 'Bagas — Muay Thai' },
  { key: 'booking', icon: 'calendar-outline', label: 'Booking Trainer' },
];

export default function PersonalTrainerScreen({ navigation }) {
  return (
    <GymSubMenuScreen
      title="Personal Trainer"
      subtitle="Latihan bareng trainer profesional"
      items={ITEMS}
      onSelect={() => {}}
      onBack={() => navigation.goBack()}
    />
  );
}