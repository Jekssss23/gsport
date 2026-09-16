export const theme = {
  colors: {
    primary: '#E60000', // Brighter red
    primaryDark: '#990000', // Deeper red
    secondary: '#000000',
    background: '#0F0F0F', // Deeper black
    surface: '#1A1A1A', // Card surface
    surfaceLight: '#252525', // Lighter surface for interaction
    text: '#FFFFFF',
    textSecondary: '#A0A0A0',
    textTertiary: '#666666',
    buttonText: '#FFFFFF',
    inputBorder: '#333333',
    success: '#00C851',
    error: '#FF4444',
    warning: '#FFBB33',
    accent: '#FF3D00',
    glass: 'rgba(255, 255, 255, 0.05)',
    glassBorder: 'rgba(255, 255, 255, 0.1)',
  },
  fonts: {
    regular: 'Humane-Regular',
    medium: 'Humane-Medium',
    bold: 'Humane-Medium', // Using Medium as bold since no Bold variant
  },
  gradients: {
    primary: ['#FF0000', '#800000'],
    dark: ['#1A1A1A', '#000000'],
    premium: ['#2A2A2A', '#0A0A0A'],
    glass: ['rgba(255, 255, 255, 0.1)', 'rgba(255, 255, 255, 0.02)'],
  },
  spacing: {
    tiny: 4,
    small: 8,
    medium: 16,
    large: 24,
    xlarge: 32,
    xxlarge: 48,
  },
  borderRadius: {
    small: 8,
    medium: 12,
    large: 20,
    xlarge: 28,
    round: 50,
  },
  shadows: {
    light: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 2 },
      shadowOpacity: 0.2,
      shadowRadius: 4,
      elevation: 3,
    },
    medium: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 4 },
      shadowOpacity: 0.3,
      shadowRadius: 8,
      elevation: 6,
    },
    heavy: {
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 8 },
      shadowOpacity: 0.5,
      shadowRadius: 16,
      elevation: 12,
    },
  }
};