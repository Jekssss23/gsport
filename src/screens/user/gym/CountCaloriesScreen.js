import React from 'react';
import { GymSubMenuScreen } from './GymSubMenuScreen';

const ITEMS = [
  { key: 'hitung', icon: 'calculator-outline', label: 'Hitung Kalori' },
  { key: 'riwayat', icon: 'time-outline', label: 'Riwayat' },
  { key: 'scan', icon: 'scan-outline', label: 'Scan Kalori' },
];

const ROUTES = {
  hitung: 'GymCalories',
  riwayat: 'GymCaloriesHistory',
  scan: 'GymScanCalories',
};

export default function CountCaloriesScreen({ navigation }) {
  return (
    <GymSubMenuScreen
      title="Count Calories"
      subtitle="Pantau kalori latihanmu"
      items={ITEMS}
      onSelect={(key) => navigation.navigate(ROUTES[key] || 'GymCalories')}
      onBack={() => navigation.goBack()}
    />
  );
}