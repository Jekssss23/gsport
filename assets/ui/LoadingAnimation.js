import React, { useEffect, useRef } from 'react';
import { View, Animated, Easing, StyleSheet } from 'react-native';

const SIZE = 80;

export default function LoadingAnimation() {
  const moveH = useRef(new Animated.Value(0)).current;
  const moveV = useRef(new Animated.Value(0)).current;
  const e7Height = useRef(new Animated.Value(0)).current;
  const e7Bottom = useRef(new Animated.Value(0)).current;
  const e7Left = useRef(new Animated.Value(0)).current;
  const e8Width = useRef(new Animated.Value(0)).current;
  const e8Left = useRef(new Animated.Value(0)).current;
  const e3Rot = useRef(new Animated.Value(0)).current;
  const e6Scale = useRef(new Animated.Value(1)).current;
  const e1Opacity = useRef(new Animated.Value(0.3)).current;
  const e2Opacity = useRef(new Animated.Value(0.8)).current;
  const e4Opacity = useRef(new Animated.Value(0.3)).current;
  const e5Opacity = useRef(new Animated.Value(0.3)).current;

  useEffect(() => {
    const loop = Animated.loop(
      Animated.parallel([
        Animated.sequence([
          Animated.timing(moveH, { toValue: 1, duration: 1200, easing: Easing.bezier(0.65, 0.05, 0.36, 1), useNativeDriver: false }),
          Animated.timing(moveH, { toValue: 0, duration: 0, useNativeDriver: false }),
        ]),
        Animated.sequence([
          Animated.timing(moveV, { toValue: 1, duration: 1200, easing: Easing.bezier(0.65, 0.05, 0.36, 1), useNativeDriver: false }),
          Animated.timing(moveV, { toValue: 0, duration: 0, useNativeDriver: false }),
        ]),
        Animated.loop(Animated.sequence([
          Animated.timing(e3Rot, { toValue: 1, duration: 800, easing: Easing.bezier(0.65, 0.05, 0.36, 1), useNativeDriver: true }),
          Animated.timing(e3Rot, { toValue: 0, duration: 0, useNativeDriver: true }),
        ])),
        Animated.loop(Animated.sequence([
          Animated.timing(e6Scale, { toValue: 1.9, duration: 800, easing: Easing.bezier(0.65, 0.05, 0.36, 1), useNativeDriver: true }),
          Animated.timing(e6Scale, { toValue: 1, duration: 800, easing: Easing.bezier(0.65, 0.05, 0.36, 1), useNativeDriver: true }),
        ])),
        Animated.loop(Animated.sequence([
          Animated.timing(e7Bottom, { toValue: 1, duration: 1000, easing: Easing.bezier(0.65, 0.05, 0.36, 1), useNativeDriver: false }),
          Animated.timing(e7Bottom, { toValue: 0, duration: 0, useNativeDriver: false }),
        ])),
        Animated.loop(Animated.sequence([
          Animated.timing(e7Left, { toValue: 1, duration: 1000, easing: Easing.bezier(0.65, 0.05, 0.36, 1), useNativeDriver: false }),
          Animated.timing(e7Left, { toValue: 0, duration: 0, useNativeDriver: false }),
        ])),
        Animated.loop(Animated.sequence([
          Animated.timing(e7Height, { toValue: 90, duration: 250, useNativeDriver: false }),
          Animated.timing(e7Height, { toValue: 0, duration: 250, useNativeDriver: false }),
          Animated.timing(e7Height, { toValue: 90, duration: 250, useNativeDriver: false }),
          Animated.timing(e7Height, { toValue: 0, duration: 250, useNativeDriver: false }),
        ])),
        Animated.loop(Animated.sequence([
          Animated.timing(e8Width, { toValue: 90, duration: 750, easing: Easing.bezier(0.65, 0.05, 0.36, 1), useNativeDriver: false }),
          Animated.timing(e8Width, { toValue: 0, duration: 750, easing: Easing.bezier(0.65, 0.05, 0.36, 1), useNativeDriver: false }),
        ])),
        Animated.loop(Animated.sequence([
          Animated.timing(e8Left, { toValue: 1, duration: 1500, easing: Easing.bezier(0.65, 0.05, 0.36, 1), useNativeDriver: false }),
          Animated.timing(e8Left, { toValue: 0, duration: 0, useNativeDriver: false }),
        ])),
        Animated.loop(Animated.sequence([
          Animated.timing(e1Opacity, { toValue: 1, duration: 200, useNativeDriver: false }),
          Animated.timing(e1Opacity, { toValue: 0.3, duration: 200, useNativeDriver: false }),
        ])),
        Animated.loop(Animated.sequence([
          Animated.timing(e2Opacity, { toValue: 1, duration: 300, useNativeDriver: false }),
          Animated.timing(e2Opacity, { toValue: 0.8, duration: 300, useNativeDriver: false }),
        ])),
        Animated.loop(Animated.sequence([
          Animated.timing(e4Opacity, { toValue: 1, duration: 200, useNativeDriver: false }),
          Animated.timing(e4Opacity, { toValue: 0.3, duration: 200, useNativeDriver: false }),
        ])),
        Animated.loop(Animated.sequence([
          Animated.timing(e5Opacity, { toValue: 1, duration: 300, useNativeDriver: false }),
          Animated.timing(e5Opacity, { toValue: 0.3, duration: 300, useNativeDriver: false }),
        ])),
      ])
    );
    loop.start();
    return () => loop.stop();
  }, []);

  const l1Top = moveH.interpolate({ inputRange: [0, 0.25, 0.5, 0.75, 1], outputRange: [0, 0, SIZE * 0.3, SIZE * 0.3, SIZE], extrapolate: 'clamp' });
  const l1Opacity = moveH.interpolate({ inputRange: [0, 0.1, 0.4, 0.6, 0.9, 1], outputRange: [0, 1, 1, 1, 1, 0], extrapolate: 'clamp' });
  const l2Left = moveV.interpolate({ inputRange: [0, 0.25, 0.5, 0.75, 1], outputRange: [0, 0, SIZE * 0.45, SIZE * 0.45, SIZE], extrapolate: 'clamp' });
  const l2Opacity = moveV.interpolate({ inputRange: [0, 0.1, 0.4, 0.6, 0.9, 1], outputRange: [0, 1, 1, 1, 1, 0], extrapolate: 'clamp' });
  const e7B = e7Bottom.interpolate({ inputRange: [0, 1], outputRange: [0, SIZE] });
  const e7L = e7Left.interpolate({ inputRange: [0, 1], outputRange: [0, SIZE] });
  const e8L = e8Left.interpolate({ inputRange: [0, 1], outputRange: [0, SIZE] });
  const e3R = e3Rot.interpolate({ inputRange: [0, 1], outputRange: ['0deg', '360deg'] });

  return (
    <View style={styles.container}>
      <View style={styles.loadingWide}>
        <Animated.View style={[styles.l1, { top: l1Top, opacity: l1Opacity }]} />
        <Animated.View style={[styles.l2, { left: l2Left, opacity: l2Opacity }]} />
        <Animated.View style={[styles.e1, { opacity: e1Opacity }]} />
        <Animated.View style={[styles.e2, { opacity: e2Opacity }]} />
        <Animated.View style={[styles.e3, { transform: [{ rotate: e3R }] }]}>
          <View style={styles.e3Text}><View style={styles.e3LineH} /><View style={styles.e3LineV} /></View>
        </Animated.View>
        <Animated.View style={[styles.e4, { opacity: e4Opacity }]} />
        <Animated.View style={[styles.e5, { opacity: e5Opacity }]} />
        <Animated.View style={[styles.e6, { transform: [{ scale: e6Scale }] }]}>
          <View style={styles.e6Dot} />
        </Animated.View>
        <Animated.View style={[styles.e7, { height: e7Height, bottom: e7B, left: e7L }]} />
        <Animated.View style={[styles.e8, { width: e8Width, left: e8L }]} />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  loadingWide: { width: SIZE, height: SIZE, justifyContent: 'center', alignItems: 'center' },
  l1: { width: 15, height: 65, position: 'absolute', backgroundColor: '#f4b400' },
  l2: { width: 15, height: 60, position: 'absolute', transform: [{ rotate: '90deg' }], backgroundColor: '#db4437' },
  e1: { width: 1, height: 40, position: 'absolute', top: 0, left: SIZE * 0.08, backgroundColor: '#0f9d58' },
  e2: { width: 60, height: 1, position: 'absolute', top: SIZE * 0.08, left: 0, backgroundColor: '#4285f4' },
  e3: { position: 'absolute', top: SIZE * 0.1, left: SIZE * 0.12 },
  e3Text: { width: 12, height: 12, justifyContent: 'center', alignItems: 'center' },
  e3LineH: { width: 12, height: 2, backgroundColor: '#4285f4', position: 'absolute' },
  e3LineV: { width: 2, height: 12, backgroundColor: '#4285f4', position: 'absolute' },
  e4: { width: 1, height: 40, position: 'absolute', top: SIZE * 0.9, right: SIZE * 0.1, backgroundColor: '#db4437' },
  e5: { width: 40, height: 1, position: 'absolute', top: SIZE, right: 0, backgroundColor: '#f4b400' },
  e6: { position: 'absolute', top: SIZE, right: 0 },
  e6Dot: { width: 10, height: 10, borderRadius: 5, backgroundColor: '#0f9d58' },
  e7: { width: 1, position: 'absolute', transform: [{ rotate: '45deg' }], backgroundColor: '#f4b400' },
  e8: { height: 1, position: 'absolute', bottom: SIZE * 0.5, left: 0, backgroundColor: '#0f9d58' },
});
