import { collection, addDoc, serverTimestamp } from 'firebase/firestore';
import { db, auth } from '../config/firebase';
import { calorieAi } from '../config/calorieAi';

const GEMINI_URL = 'https://generativelanguage.googleapis.com/v1beta/models/';

// Coba model berurutan (beberapa model bisa tidak tersedia untuk akun baru → 404)
const GEMINI_MODELS = [
  calorieAi.geminiModel || 'gemini-3.6-flash',
  'gemini-3.5-flash-lite',
  'gemini-3.1-flash-lite',
];

const NOT_FOOD_MSG = 'Harap foto makanan kamu :)';

const DETECT_PROMPT = `Analisis foto makanan ini. Deteksi setiap makanan utama yang terlihat.
Untuk setiap makanan berikan:
- name: nama makanan dalam Bahasa Indonesia
- portion_grams: estimasi berat porsi dalam gram (angka)
- calories: estimasi kalori dalam kkal (angka)
- confidence: tingkat keyakinan 0 sampai 1

Balas HANYA dengan JSON, tanpa teks lain, format:
{"items":[{"name":"...","portion_grams":0,"calories":0,"confidence":0}],"error":null}
Jika foto tidak menunjukkan makanan yang jelas, balas:
{"items":[],"error":"pesan singkat kenapa tidak terdeteksi"}`;

const NUTRIENT_ENERGY_IDS = new Set([1008, 9003, 208, 1004]);

async function detectWithGemini(base64) {
  const errors = [];
  for (const model of GEMINI_MODELS) {
    try {
      const url = `${GEMINI_URL}${model}:generateContent?key=${calorieAi.geminiApiKey}`;
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          contents: [
            {
              role: 'user',
              parts: [
                { text: DETECT_PROMPT },
                { inline_data: { mime_type: 'image/jpeg', data: base64 } },
              ],
            },
          ],
          generationConfig: {
            responseMimeType: 'application/json',
            temperature: 0.2,
          },
        }),
      });

      if (!res.ok) {
        // Ambil pesan error detail dari body Gemini agar kelihatan jelas
        let detail = '';
        try {
          const body = await res.json();
          detail = body?.error?.message || '';
        } catch {}
        errors.push(`${model}: ${res.status} ${detail}`.trim());
        continue; // coba model berikutnya
      }

      const json = await res.json();
      const content = json?.candidates?.[0]?.content;
      // Model "thinking" menyisipkan bagian thought → ambil teks jawaban pertama
      const text = content?.parts?.find((p) => p.text && !p.thought)?.text;
      if (!text) {
        errors.push(`${model}: response kosong`);
        continue;
      }

      // Gempaan: kadang ada backticks / prefix pembungkus tambahan
      const cleaned = text.replace(/```json|```/g, '').trim();
      return JSON.parse(cleaned);
    } catch (e) {
      // JSON parse error → response bukan JSON yang valid
      if (e instanceof SyntaxError) {
        errors.push(`${model}: response bukan JSON valid`);
        continue;
      }
      throw e;
    }
  }
  throw new Error(
    `Gemini gagal di semua model.\nDetail: ${errors.join('\n')}` +
      '\nCek: API key benar? (format AIza...)'
  );
}

async function refineWithUsda(name) {
  if (!calorieAi.usdaApiKey) {
    return { refined: false, note: 'USDA key kosong' };
  }
  try {
    const url =
      `https://api.nal.usda.gov/fdc/v1/foods/search?api_key=${calorieAi.usdaApiKey}` +
      `&query=${encodeURIComponent(name)}&pageSize=${calorieAi.usdaPerPage}&dataType=SR Legacy,Foundation,FNDDS`;
    const res = await fetch(url);
    if (!res.ok) return { refined: false, note: `USDA status ${res.status}` };
    const json = await res.json();
    const food = json?.foods?.[0];
    if (!food) return { refined: false, note: 'Tidak ada match di USDA' };

    const nutrient = food.foodNutrients?.find((n) => NUTRIENT_ENERGY_IDS.has(n.nutrientId));
    const kcalPer100g = nutrient ? Number(nutrient.value) : null;
    return { refined: Boolean(kcalPer100g), kcalPer100g };
  } catch {
    return { refined: false, note: 'USDA gagal diakses' };
  }
}

/**
 * Analisis foto makanan (base64) → estimasi kalori.
 * Hybrid: Gemini detect + porsi, USDA koreksi kalori bila match.
 * Mengembalikan: { items, totalCalories, source }
 */
export async function analyzeFoodPhoto(base64) {
  if (!calorieAi.ready) {
    throw new Error('Gemini API key belum diisi di src/config/calorieAi.js');
  }

  const parsed = await detectWithGemini(base64);
  if (parsed.error) {
    return { items: [], totalCalories: 0, source: parsed.error, isError: true };
  }

  const items = [];
  let totalCalories = 0;
  for (const item of parsed.items || []) {
    const entry = {
      name: item.name,
      portionGrams: Number(item.portion_grams) || 0,
      calories: Math.round(Number(item.calories) || 0),
      confidence: Number(item.confidence) || 0,
      refined: false,
      sourceDB: 'gemini',
    };

    if (item.name) {
      const usda = await refineWithUsda(item.name);
      if (usda.refined && usda.kcalPer100g && entry.portionGrams > 0) {
        entry.calories = Math.round((entry.portionGrams / 100) * usda.kcalPer100g);
        entry.refined = true;
        entry.sourceDB = 'usda';
      }
    }

    totalCalories += entry.calories;
    items.push(entry);
  }

  if (!items.length) {
    return { items: [], totalCalories: 0, source: NOT_FOOD_MSG, isError: true };
  }

  return { items, totalCalories, isError: false };
}

/**
 * Simpan hasil scan ke Firestore collection calorie_history/{doc}.
 * Hanya teks (nama + kalori), GAMBAR TIDAK DISIMPAN.
 */
export async function saveCalorieRecord(record) {
  const uid = auth.currentUser?.uid;
  if (!uid) throw new Error('User tidak terautentikasi');

  const docRef = await addDoc(collection(db, 'calorie_history'), {
    uid,
    foods: record.items,
    totalCalories: record.totalCalories,
    note: '',
    createdAt: serverTimestamp(),
  });
  return docRef.id;
}