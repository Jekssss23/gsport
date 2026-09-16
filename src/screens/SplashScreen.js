import React, { useState, useRef, useEffect, useImperativeHandle, forwardRef } from 'react';
import { View, StyleSheet, Dimensions, TouchableOpacity, Text, ScrollView, Animated } from 'react-native';
import { StatusBar } from 'expo-status-bar';
import { Image } from 'expo-image';
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
  const { navigation } = props;
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isAnimating, setIsAnimating] = useState(false);

  const scrollViewRef = useRef(null);
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideUpAnim = useRef(new Animated.Value(50)).current;

  const isLastSlide = currentIndex === SLIDES.length - 1;

  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 800,
        useNativeDriver: true,
      }),
      Animated.timing(slideUpAnim, {
        toValue: 0,
        duration: 800,
        useNativeDriver: true,
      }),
    ]).start();
  }, []);

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

  const handleNext = () => {
    if (currentIndex < SLIDES.length - 1) {
      scrollViewRef.current?.scrollTo({
        x: (currentIndex + 1) * width,
        y: 0,
        animated: true,
      });
    } else {
      navigation.replace('Login');
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

  const handleGetStarted = () => {
    navigation.replace('Login');
  };

  const onMomentumScrollEnd = (e) => {
    const index = Math.round(e.nativeEvent.contentOffset.x / width);
    if (index !== currentIndex) {
      setCurrentIndex(index);
    }
  };

  const renderSlide = (slide, index) => (
    <View key={index} style={styles.slide}>
      <Image
        source={slide.image}
        style={styles.imageBackground}
        contentFit="cover"
        transition={200}
        cachePolicy="memory-disk"
      />
      <View style={styles.overlay} />
      <View style={styles.slideContent}>
        <Animated.View style={[
          styles.textContent,
          {
            opacity: fadeAnim,
            transform: [{ translateY: slideUpAnim }],
          },
        ]}>
          <Image
            source={LOGO}
            style={styles.logo}
            contentFit="contain"
            transition={200}
            cachePolicy="memory-disk"
          />
          <Text style={styles.slideTitle}>{slide.title}</Text>
          <Text style={styles.slideSubtitle}>{slide.subtitle}</Text>
        </Animated.View>
      </View>
    </View>
  );

  return (
    <View style={styles.container}>
      <StatusBar style="light" translucent backgroundColor="transparent" />

      <ScrollView
        ref={scrollViewRef}
        horizontal
        pagingEnabled
        showsHorizontalScrollIndicator={false}
        onMomentumScrollEnd={onMomentumScrollEnd}
        scrollEventThrottle={16}
        style={styles.scrollView}
        contentContainerStyle={styles.scrollContent}
        scrollEnabled={true}
      >
        {SLIDES.map(renderSlide)}
      </ScrollView>

      {/* Bottom Controls */}
      <View style={styles.bottomSection}>
        {/* Page Indicators */}
        <View style={styles.indicatorsContainer}>
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

        {/* Navigation Buttons */}
        <View style={styles.buttonRow}>
          {/* Back Arrow */}
          <TouchableOpacity
            onPress={handleBack}
            disabled={currentIndex === 0 || isAnimating}
            activeOpacity={0.6}
            hitSlop={{ top: 25, bottom: 25, left: 25, right: 25 }}
          >
            <Text style={[
              styles.arrowText,
              currentIndex === 0 && styles.arrowTextDisabled,
            ]}>
              ‹
            </Text>
          </TouchableOpacity>

          {/* Center Button: Skip (pages 1-3) or Get Started (page 4) */}
          {isLastSlide ? (
            <TouchableOpacity
              style={styles.getStartedButton}
              onPress={handleGetStarted}
              activeOpacity={0.85}
            >
              <Text style={styles.getStartedText}>Get Started</Text>
            </TouchableOpacity>
          ) : (
            <TouchableOpacity
              onPress={() => navigation.replace('Login')}
              hitSlop={{ top: 15, bottom: 15, left: 15, right: 15 }}
            >
              <Text style={styles.skipText}>Skip</Text>
            </TouchableOpacity>
          )}

          {/* Forward Arrow (hidden on last page) */}
          {!isLastSlide && (
            <TouchableOpacity
              onPress={handleNext}
              disabled={isAnimating}
              activeOpacity={0.6}
              hitSlop={{ top: 25, bottom: 25, left: 25, right: 25 }}
            >
              <Text style={styles.arrowText}>›</Text>
            </TouchableOpacity>
          )}
          {isLastSlide && <View style={styles.arrowPlaceholder} />}
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
  scrollView: {
    flex: 1,
  },
  scrollContent: {
    flexDirection: 'row',
  },
  slide: {
    width,
    height: '100%',
  },
  imageBackground: {
    ...StyleSheet.absoluteFillObject,
  },
  overlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.55)',
  },
  slideContent: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 40,
    paddingBottom: 160,
  },
  textContent: {
    alignItems: 'center',
    maxWidth: width * 0.85,
  },
  logo: {
    width: 180,
    height: 60,
    marginBottom: 24,
    opacity: 0.95,
  },
  slideTitle: {
    color: 'white',
    fontSize: 44,
    fontFamily: theme.fonts.medium,
    textAlign: 'center',
    letterSpacing: 2,
    lineHeight: 52,
    textShadowColor: 'rgba(0,0,0,0.5)',
    textShadowOffset: { width: 0, height: 2 },
    textShadowRadius: 4,
  },
  slideSubtitle: {
    color: 'rgba(255,255,255,0.9)',
    fontSize: 17,
    fontWeight: '500',
    textAlign: 'center',
    letterSpacing: 0.8,
    marginTop: 18,
    lineHeight: 26,
    textShadowColor: 'rgba(0,0,0,0.4)',
    textShadowOffset: { width: 0, height: 1 },
    textShadowRadius: 3,
  },
  bottomSection: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    paddingHorizontal: 28,
    paddingBottom: 50,
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
  arrowText: {
    color: 'white',
    fontSize: 40,
    fontWeight: '200',
    lineHeight: 40,
    includeFontPadding: false,
    textAlign: 'center',
  },
  arrowTextDisabled: {
    color: 'rgba(255,255,255,0.3)',
  },
  arrowPlaceholder: {
    width: 40,
  },
  skipButton: {
    paddingVertical: 12,
    paddingHorizontal: 8,
  },
  skipText: {
    color: 'rgba(255,255,255,0.55)',
    fontSize: 14,
    fontWeight: '300',
    letterSpacing: 0.5,
  },
  getStartedButton: {
    backgroundColor: theme.colors.primary,
    paddingVertical: 16,
    paddingHorizontal: 48,
    borderRadius: theme.borderRadius.round,
    ...theme.shadows.medium,
  },
  getStartedText: {
    color: 'white',
    fontSize: 16,
    fontFamily: theme.fonts.medium,
    letterSpacing: 1,
  },
});