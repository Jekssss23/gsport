import React from 'react';
import { View, StyleSheet, Dimensions } from 'react-native';
import LottieView from 'lottie-react-native';
import { theme } from '../styles/theme';

const { width, height } = Dimensions.get('window');

export default function LoadingScreen() {
  return (
    <View style={styles.container}>
      <LottieView
        source={require('../../assets/lottie/kickball.json')}
        autoPlay
        loop
        style={styles.lottie}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
    justifyContent: 'center',
    alignItems: 'center',
  },
  lottie: {
    width: width * 0.8,
    height: width * 0.8,
  },
});
