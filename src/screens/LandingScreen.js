import React from 'react';
import { View, StyleSheet, Dimensions, TouchableOpacity, Text } from 'react-native';
import { StatusBar } from 'expo-status-bar';

const { width, height } = Dimensions.get('window');

export default function LandingScreen({ navigation }) {
  return (
    <View style={styles.container}>
      
      {/* Temporary placeholder for video */}
      <View style={styles.videoContainer}>
        <View style={styles.videoPlaceholder}>
          <Text style={styles.placeholderText}>GSC Sports Center</Text>
        </View>
      </View>

      {/* Invisible touchable area to navigate */}
      <TouchableOpacity 
        style={styles.touchableOverlay}
        onPress={() => navigation.navigate('Login')}
        activeOpacity={1}
      >
         <View style={styles.tapIndicator}>
             <Text style={styles.tapText}>Tap anywhere to continue</Text>
         </View>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: 'black',
    justifyContent: 'center',
    alignItems: 'center',
  },
  videoContainer: {
    width: width,
    height: height, // Occupy full screen, let resizeMode handle the rest
    justifyContent: 'center',
    alignItems: 'center',
  },
  video: {
    width: '100%',
    height: '100%',
  },
  videoPlaceholder: {
    width: '100%',
    height: '100%',
    backgroundColor: '#000',
    justifyContent: 'center',
    alignItems: 'center',
  },
  placeholderText: {
    color: 'white',
    fontSize: 32,
    fontWeight: 'bold',
    textAlign: 'center',
  },
  touchableOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    justifyContent: 'flex-end',
    alignItems: 'center',
    paddingBottom: 50,
  },
  tapIndicator: {
    opacity: 0.5,
  },
  tapText: {
    color: 'white',
    fontSize: 14,
    letterSpacing: 2,
    textTransform: 'uppercase',
  },
});
