import React, { useEffect, useRef } from 'react';
import { View, StyleSheet, Dimensions, TouchableOpacity, Text, Animated, Image } from 'react-native';
import { StatusBar } from 'expo-status-bar';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../styles/theme';
import { APP_LOGO_PRIMARY } from '../constants/assets';

const { width, height } = Dimensions.get('window');

export default function LandingScreen({ navigation }) {
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideAnim = useRef(new Animated.Value(30)).current;
  const scaleAnim = useRef(new Animated.Value(0.9)).current;

  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 1500,
        useNativeDriver: true,
      }),
      Animated.timing(slideAnim, {
        toValue: 0,
        duration: 1200,
        useNativeDriver: true,
      }),
      Animated.spring(scaleAnim, {
        toValue: 1,
        friction: 8,
        tension: 40,
        useNativeDriver: true,
      }),
    ]).start();
  }, []);

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      
      {/* Background with Dark Gradient and subtle overlay */}
      <LinearGradient
        colors={[theme.colors.background, theme.colors.secondary]}
        style={StyleSheet.absoluteFill}
      />
      
      {/* Visual content */}
      <View style={styles.content}>
        <View style={styles.glow} />
        <Animated.View style={[
          styles.logoContainer,
          {
            opacity: fadeAnim,
            transform: [{ scale: scaleAnim }]
          }
        ]}>
          <Image source={APP_LOGO_PRIMARY} style={styles.logoImage} resizeMode="contain" />
        </Animated.View>

        <Animated.View style={[
          styles.textContainer,
          {
            opacity: fadeAnim,
            transform: [{ translateY: slideAnim }]
          }
        ]}>
          <Text style={styles.title}>G SPORTS CENTER</Text>
          <View style={styles.separator} />
          <Text style={styles.subtitle}>PREMIUM SPORTS EXPERIENCE</Text>
          <Text style={styles.subtitle2}>Futsal • Badminton • Pickleball • Class</Text>
        </Animated.View>
      </View>

      {/* Action Area */}
      <TouchableOpacity 
        style={styles.touchableOverlay}
        onPress={() => navigation.navigate('Login')}
        activeOpacity={0.9}
      >
         <Animated.View style={[
           styles.tapIndicator,
           { opacity: fadeAnim }
         ]}>
             <LinearGradient
               colors={theme.gradients.primary}
               start={{ x: 0, y: 0 }}
               end={{ x: 1, y: 0 }}
               style={styles.button}
             >
                <Text style={styles.buttonText}>GET STARTED</Text>
             </LinearGradient>
         </Animated.View>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: 'black',
  },
  content: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 40,
  },
  logoContainer: {
    marginBottom: 40,
    alignItems: 'center',
  },
  logoImage: {
    width: 210,
    height: 90,
  },
  textContainer: {
    alignItems: 'center',
  },
  title: {
    color: 'white',
    fontSize: 30,
    fontWeight: '800',
    textAlign: 'center',
    letterSpacing: 4,
  },
  separator: {
    width: 40,
    height: 3,
    backgroundColor: theme.colors.primary,
    marginVertical: 20,
    borderRadius: 2,
  },
  subtitle: {
    color: theme.colors.textSecondary,
    fontSize: 14,
    letterSpacing: 3,
    textAlign: 'center',
    textTransform: 'uppercase',
  },
  subtitle2: {
    color: 'rgba(255,255,255,0.55)',
    fontSize: 11,
    letterSpacing: 2,
    marginTop: 10,
    textAlign: 'center',
  },
  glow: {
    position: 'absolute',
    width: 260,
    height: 260,
    borderRadius: 130,
    backgroundColor: 'rgba(230,0,0,0.12)',
    top: '26%',
  },
  touchableOverlay: {
    position: 'absolute',
    bottom: 60,
    left: 0,
    right: 0,
    alignItems: 'center',
  },
  tapIndicator: {
    width: width * 0.7,
  },
  button: {
    paddingVertical: 18,
    borderRadius: theme.borderRadius.round,
    alignItems: 'center',
    ...theme.shadows.medium,
  },
  buttonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
    letterSpacing: 2,
  },
});
