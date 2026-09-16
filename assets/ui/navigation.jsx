import React, { useEffect, useRef } from 'react';
import { Animated, Pressable, StyleSheet, Text, View, Easing } from 'react-native';
import { Ionicons } from '@expo/vector-icons';

const COLORS = {
  bg: '#0F0F0F',
  surface: '#1A1A1A',
  surfaceLight: '#252525',
  primary: '#E60000',
  text: '#FFFFFF',
  textSecondary: '#A0A0A0',
  border: 'rgba(255, 255, 255, 0.12)',
};

const pointOnCircle = (i, n, r, spanDeg = 160) => {
  // Distribusi arc mengarah ke atas (dari kiri -> atas -> kanan),
  // cocok untuk FAB di dock bawah. Full 360° akan memotong layar.
  const start = 180;
  const theta = (start - spanDeg * (i / (n - 1))) * (Math.PI / 180);
  return { x: r * Math.cos(theta), y: -r * Math.sin(theta) };
};

/**
 * CircleMenu — tombol pusat (trigger) yang membuka menu radial mengarah ke atas.
 * Dibuat dalam mode controlled:
 *  - items: Array<{ key, label, icon, color? }>
 *  - open: boolean (dikelola parent)
 *  - onOpenChange(open: boolean): dipanggil saat trigger ditekan
 *  - onSelect(key): dipanggil saat menu dipilih
 *  - activeKey: key menu aktif (highlight)
 *  - triggerSize, itemSize, radius, iconColor, triggerColor
 */
export function CircleMenu({
  items,
  activeKey,
  open = false,
  onOpenChange,
  onSelect,
  triggerSize = 56,
  itemSize = 52,
  radius = 106,
  iconColor = '#FFFFFF',
  triggerColor = COLORS.primary,
}) {
  const itemAnim = useRef(items.map(() => new Animated.Value(0))).current;
  const triggerAnim = useRef(new Animated.Value(0)).current;

  const openAnim = () => {
    Animated.parallel([
      Animated.timing(triggerAnim, {
        toValue: 1,
        duration: 240,
        easing: Easing.out(Easing.quad),
        useNativeDriver: true,
      }),
      Animated.stagger(
        45,
        itemAnim.map((a) =>
          Animated.spring(a, {
            toValue: 1,
            stiffness: 300,
            damping: 24,
            mass: 0.9,
            useNativeDriver: true,
          })
        )
      ),
    ]).start();
  };

  const closeAnim = () => {
    Animated.parallel([
      Animated.timing(triggerAnim, {
        toValue: 0,
        duration: 200,
        easing: Easing.in(Easing.quad),
        useNativeDriver: true,
      }),
      Animated.parallel(
        itemAnim.map((a) =>
          Animated.timing(a, {
            toValue: 0,
            duration: 180,
            useNativeDriver: true,
          })
        )
      ),
    ]).start();
  };

  // jalankan animasi sesuai prop `open`
  useEffect(() => {
    if (open) {
      openAnim();
    } else {
      closeAnim();
    }
  }, [open]);

  useEffect(() => {
    return () => {
      itemAnim.forEach((a) => a.stopAnimation());
      triggerAnim.stopAnimation();
    };
  }, []);

  const toggle = () => {
    onOpenChange && onOpenChange(!open);
  };

  const pick = (key) => {
    onOpenChange && onOpenChange(false);
    onSelect && onSelect(key);
  };

  const triggerSpin = triggerAnim.interpolate({
    inputRange: [0, 1],
    outputRange: ['0deg', '135deg'],
  });

  // posisi awal item (pusat = trigger)
  const originX = (triggerSize - itemSize) / 2;
  const originY = (triggerSize - itemSize) / 2;

  return (
    <View style={[styles.menuContainer, { width: triggerSize, height: triggerSize }]} pointerEvents="box-none">
      {open && <Pressable style={StyleSheet.absoluteFill} onPress={() => onOpenChange && onOpenChange(false)} />}

      {items.map((item, i) => {
        const pos = pointOnCircle(i, items.length, radius);
        const isActive = activeKey === item.key;
        const translateX = itemAnim[i].interpolate({
          inputRange: [0, 1],
          outputRange: [0, pos.x],
        });
        const translateY = itemAnim[i].interpolate({
          inputRange: [0, 1],
          outputRange: [0, pos.y],
        });
        const opacity = itemAnim[i].interpolate({
          inputRange: [0, 0.15, 1],
          outputRange: [0, 1, 1],
        });
        const scale = itemAnim[i].interpolate({
          inputRange: [0, 1],
          outputRange: [0.5, 1],
        });
        const labelOpacity = itemAnim[i];

        return (
          <Animated.View
            key={item.key}
            style={[
              styles.itemWrap,
              {
                left: originX,
                top: originY,
                width: itemSize,
                height: itemSize,
                transform: [{ translateX }, { translateY }],
                opacity,
              },
            ]}
          >
            <Animated.View style={{ width: itemSize, height: itemSize, transform: [{ scale }] }}>
              <Pressable
                onPress={() => pick(item.key)}
                style={[
                  styles.itemBase,
                  isActive ? styles.itemActive : styles.itemIdle,
                  isActive && { backgroundColor: item.color || triggerColor },
                ]}
              >
                <Ionicons name={item.icon} size={22} color={isActive ? '#fff' : COLORS.text} />
              </Pressable>
            </Animated.View>
            <Animated.View style={[styles.labelWrap, { top: itemSize + 2, opacity: labelOpacity }]}>
              <Text style={styles.label}>{item.label}</Text>
            </Animated.View>
          </Animated.View>
        );
      })}

      <Animated.View style={{ transform: [{ rotate: triggerSpin }] }}>
        <Pressable
          onPress={toggle}
          style={[styles.trigger, { width: triggerSize, height: triggerSize, backgroundColor: triggerColor }]}
        >
          <Ionicons name={open ? 'close' : 'grid'} size={26} color={iconColor} />
        </Pressable>
      </Animated.View>
    </View>
  );
}

// tap di area kosong menu container = tutup menu

const styles = StyleSheet.create({
  menuContainer: {
    alignItems: 'center',
    justifyContent: 'center',
  },
  itemWrap: {
    position: 'absolute',
    alignItems: 'center',
    justifyContent: 'center',
  },
  itemBase: {
    width: '100%',
    height: '100%',
    borderRadius: 99,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: COLORS.border,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.4,
    shadowRadius: 6,
    elevation: 7,
  },
  itemIdle: {
    backgroundColor: COLORS.surfaceLight,
  },
  itemActive: {
    borderColor: 'rgba(255,255,255,0.25)',
  },
  labelWrap: {
    position: 'absolute',
    right: 0,
    left: 0,
    alignItems: 'center',
  },
  label: {
    color: COLORS.text,
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 0.3,
    textAlign: 'center',
    backgroundColor: 'rgba(15,15,15,0.85)',
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 10,
    overflow: 'hidden',
  },
  trigger: {
    borderRadius: 99,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 2,
    borderColor: 'rgba(255,255,255,0.6)',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.5,
    shadowRadius: 8,
    elevation: 12,
  },
});