import React, { useState, useRef, useImperativeHandle, forwardRef } from 'react';
import { View, StyleSheet, Dimensions, TouchableOpacity, Text, ScrollView, Animated, Image } from 'react-native';
import { StatusBar } from 'expo-status-bar';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons } from '@expo/vector-icons';
import { useNavigation } from '@react-navigation/native';
import { theme } from '../styles/theme';

const { width, height } = Dimensions.get('window');

const SLIDES = [
  {
    image: require('../../assets/images/pickle.jpg'),
    title: 'PICKLEBALL COURTS',
    subtitle: 'Premium indoor & outdoor courts with professional lighting',
  },
  {
    image: require('../../assets/images/gym2.jpg'),
    title: 'FITNESS & GYM',
    subtitle: 'Modern equipment with certified trainers available',
  },
  {
    image: require('../../assets/images/swim.jpg'),
    title: 'SWIMMING POOL',
    subtitle: 'Olympic-standard pool with temperature control',
  },
  {
    image: require('../../assets/images/muaythai.jpg'),
    title: 'MUAY THAI',
    subtitle: 'Authentic training with professional coaches',
  },
];

const LOGO = require('../../assets/LOGO/LOGO GSPORT (Full PUTIH).png');

const SplashScreen = forwardRef((props, ref) => {
  const navigation = useNavigation();
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isAnimating, setIsAnimating] = useState(false);

  const scrollViewRef = useRef(null);
  const isLastSlide = currentIndex === SLIDES.length - 1;

  useImperativeHandle(ref, () => ({
    scrollToSlide: (index) => {
      if (scrollViewRef.current && index >= 0 && index < SLIDES.length && index !== currentIndex) {
        setIsAnimating(true);
        setCurrentIndex(index);
        scrollViewRef.current.scrollTo({
          x: index * width,
          y: 0,
          animated: true,
        });
        setTimeout(() => setIsAnimating(false), 400);
      }
    },
  }));

  const handleNavigateToLogin = () => {
    try {
      if (navigation && typeof navigation.replace === 'function') {
        navigation.replace('Login');
      } else {
        navigation.navigate('Login');
      }
    } catch (err) {
      console.warn('Navigation to Login error:', err);
      try {
        navigation.navigate('Login');
      } catch (e) {}
    }
  };

  const handleNext = () => {
    if (currentIndex < SLIDES.length - 1) {
      scrollViewRef.current?.scrollTo({
        x: (currentIndex + 1) * width,
        y: 0,
        animated: true,
      });
    } else {
      handleNavigateToLogin();
    }
  };

  const handleBack = () => {
    if (currentIndex > 0) {
      scrollViewRef.current?.scrollTo({
        x: (currentIndex - 1) * width,
        y: 0,
        animated: true,
      });
    }
  };

  const onMomentumScrollEnd = (e) => {
    const index = Math.round(e.nativeEvent.contentOffset.x / width);
    if (index !== currentIndex) {
      setCurrentIndex(index);
    }
  };

  return (
    <View style={styles.container}>
      <StatusBar style="light" translucent backgroundColor="transparent" />

      {/* Fullscreen Photo Background Slider */}
      <ScrollView
        ref={scrollViewRef}
        horizontal
        pagingEnabled
        showsHorizontalScrollIndicator={false}
        onMomentumScrollEnd={onMomentumScrollEnd}
        scrollEventThrottle={16}
        style={StyleSheet.absoluteFillObject}
        contentContainerStyle={styles.scrollContent}
        scrollEnabled={true}
      >
        {SLIDES.map((slide, index) => (
          <View key={index} style={styles.slide}>
            <Image
              source={slide.image}
              style={StyleSheet.absoluteFillObject}
              resizeMode="cover"
            />
            {/* Subtle Gradient Overlay so white text is readable over photo */}
            <LinearGradient
              colors={['rgba(0,0,0,0.35)', 'rgba(0,0,0,0.1)', 'rgba(0,0,0,0.7)']}
              locations={[0, 0.4, 1]}
              style={StyleSheet.absoluteFillObject}
            />
          </View>
        ))}
      </ScrollView>

      {/* Top Right Blue Settings Icon */}
      <TouchableOpacity
        style={styles.settingsButton}
        activeOpacity={0.8}
        onPress={handleNavigateToLogin}
      >
        <Ionicons name="settings-sharp" size={22} color="white" />
      </TouchableOpacity>

      {/* Floating Center Section: Logo + Title + Subtitle */}
      <View style={styles.centerSection} pointerEvents="none">
        <Image
          source={LOGO}
          style={styles.logo}
          resizeMode="contain"
        />
        <Text style={styles.slideTitle}>
          {SLIDES[currentIndex].title}
        </Text>
        <Text style={styles.slideSubtitle}>
          {SLIDES[currentIndex].subtitle}
        </Text>
      </View>

      {/* Bottom Section: Indicators + Navigation Controls */}
      <View style={styles.bottomSection} pointerEvents="box-none">
        {/* Page Indicators */}
        <View style={styles.indicatorsContainer} pointerEvents="none">
          {SLIDES.map((_, i) => (
            <Animated.View
              key={i}
              style={[
                styles.indicator,
                {
                  width: i === currentIndex ? 24 : 8,
                  backgroundColor: i === currentIndex ? theme.colors.primary : 'rgba(255,255,255,0.4)',
                },
              ]}
            />
          ))}
        </View>

        {/* Navigation Controls */}
        <View style={styles.buttonRow}>
          {/* Back Arrow */}
          <TouchableOpacity
            onPress={handleBack}
            disabled={currentIndex === 0 || isAnimating}
            activeOpacity={0.6}
            style={styles.arrowButton}
            hitSlop={{ top: 25, bottom: 25, left: 25, right: 25 }}
          >
            <Ionicons
              name="chevron-back"
              size={26}
              color={currentIndex === 0 ? 'rgba(255,255,255,0.3)' : 'white'}
            />
          </TouchableOpacity>

          {/* Skip or Get Started */}
          {isLastSlide ? (
            <TouchableOpacity
              style={styles.getStartedButton}
              onPress={handleNavigateToLogin}
              activeOpacity={0.8}
            >
              <Text style={styles.getStartedText}>Get Started</Text>
            </TouchableOpacity>
          ) : (
            <TouchableOpacity
              style={styles.skipButton}
              onPress={handleNavigateToLogin}
              activeOpacity={0.8}
              hitSlop={{ top: 15, bottom: 15, left: 15, right: 15 }}
            >
              <Text style={styles.skipText}>Skip</Text>
            </TouchableOpacity>
          )}

          {/* Forward Arrow */}
          {!isLastSlide ? (
            <TouchableOpacity
              onPress={handleNext}
              disabled={isAnimating}
              activeOpacity={0.6}
              style={styles.arrowButton}
              hitSlop={{ top: 25, bottom: 25, left: 25, right: 25 }}
            >
              <Ionicons name="chevron-forward" size={26} color="white" />
            </TouchableOpacity>
          ) : (
            <View style={styles.arrowPlaceholder} />
          )}
        </View>
      </View>
    </View>
  );
});

SplashScreen.displayName = 'SplashScreen';

export default SplashScreen;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: 'black',
  },
  scrollContent: {
    flexDirection: 'row',
  },
  slide: {
    width,
    height,
  },
  settingsButton: {
    position: 'absolute',
    top: 50,
    right: 20,
    zIndex: 100,
    elevation: 100,
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: '#007AFF',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 4,
  },
  centerSection: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 220,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 24,
    zIndex: 20,
  },
  logo: {
    width: 140,
    height: 40,
    marginBottom: 20,
    opacity: 0.95,
  },
  slideTitle: {
    fontFamily: theme.fonts.medium,
    color: '#FFFFFF',
    fontSize: 44,
    lineHeight: 48,
    textAlign: 'center',
    letterSpacing: 2,
    textTransform: 'uppercase',
    marginBottom: 12,
  },
  slideSubtitle: {
    color: 'rgba(255, 255, 255, 0.95)',
    fontSize: 15,
    fontWeight: '400',
    textAlign: 'center',
    letterSpacing: 0.4,
    lineHeight: 22,
    maxWidth: 320,
  },
  bottomSection: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    paddingHorizontal: 28,
    paddingBottom: 48,
    zIndex: 100,
    elevation: 100,
  },
  indicatorsContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 36,
    minHeight: 12,
  },
  indicator: {
    height: 8,
    borderRadius: 4,
    marginHorizontal: 4,
  },
  buttonRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  arrowButton: {
    padding: 8,
    justifyContent: 'center',
    alignItems: 'center',
  },
  arrowPlaceholder: {
    width: 36,
  },
  skipButton: {
    paddingVertical: 10,
    paddingHorizontal: 16,
  },
  skipText: {
    color: 'rgba(255,255,255,0.7)',
    fontSize: 15,
    fontWeight: '400',
    letterSpacing: 0.5,
  },
  getStartedButton: {
    backgroundColor: theme.colors.primary,
    paddingVertical: 14,
    paddingHorizontal: 40,
    borderRadius: theme.borderRadius.round,
    ...theme.shadows.medium,
  },
  getStartedText: {
    color: 'white',
    fontSize: 16,
    fontWeight: '600',
    letterSpacing: 1,
  },
});