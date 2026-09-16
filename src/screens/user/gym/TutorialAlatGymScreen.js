import React from 'react';
import { GymSubMenuScreen } from './GymSubMenuScreen';

const ITEMS = [
  { key: 'dumbbell', icon: 'barbell-outline', label: 'Barbell & Dumbbell' },
  { key: 'treadmill', icon: 'walk-outline', label: 'Treadmill & Cardio' },
  { key: 'machine', icon: 'construct-outline', label: 'Leg Press & Machine' },
  { key: 'cable', icon: 'git-network-outline', label: 'Cable & Pulley' },
  { key: 'stretching', icon: 'body-outline', label: 'Stretching & Mobility' },
];

export default function TutorialAlatGymScreen({ navigation }) {
  return (
    <GymSubMenuScreen
      title="Tutorial Alat Gym"
      subtitle="Panduan penggunaan alat"
      items={ITEMS}
      onSelect={() => {}}
      onBack={() => navigation.goBack()}
    />
  );
}