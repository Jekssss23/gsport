import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

export default function ClassScheduleScreen() {
  return (
    <View style={styles.container}>
      <Text>Class Schedule Screen</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, justifyContent: 'center', alignItems: 'center' }
});
