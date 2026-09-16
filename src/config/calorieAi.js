/**
 * Konfigurasi AI untuk fitur Scan Kalori (food recognition).
 *
 * PENTING: Isi API key di .env (EXPO_PUBLIC_*) ATAU langsung di bawah ini.
 * Contoh .env:
 *   EXPO_PUBLIC_GEMINI_API_KEY=AIza...
 *   EXPO_PUBLIC_USDA_API_KEY=DEMO_KEY
 */
export const calorieAi = {
  geminiApiKey: process.env.EXPO_PUBLIC_GEMINI_API_KEY || 'AQ.Ab8RN6LrPRFlc1w1zeAxvdFCQOP5az_k4INO2abeo0g15pA2oQ',
  geminiModel: 'gemini-3.6-flash',
  usdaApiKey: process.env.EXPO_PUBLIC_USDA_API_KEY || 'XT8Kg6y2GgR1lflL9wwBiQsakgi4zCyO2Lx4nS1l',
  usdaPerPage: 1,

  get ready() {
    return Boolean(this.geminiApiKey);
  },
};