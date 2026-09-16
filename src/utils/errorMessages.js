export function getUserFriendlyErrorMessage(error, fallback = 'Terjadi kesalahan. Silakan coba lagi.') {
  const raw = (error?.message || '').toLowerCase();

  if (
    raw.includes('network request failed') ||
    raw.includes('failed to fetch') ||
    raw.includes('timeout') ||
    raw.includes('socket')
  ) {
    return 'Tidak bisa terhubung ke internet. Cek koneksi kamu lalu coba lagi.';
  }

  if (raw.includes('permission')) {
    return 'Akses dibutuhkan agar fitur ini berjalan. Coba izinkan aksesnya dulu.';
  }

  if (raw.includes('not found') || raw.includes('404')) {
    return 'Data tidak ditemukan.';
  }

  if (raw.includes('401') || raw.includes('403') || raw.includes('unauthorized')) {
    return 'Sesi kamu sudah tidak valid. Silakan login ulang.';
  }

  if (raw.includes('500') || raw.includes('server')) {
    return 'Server sedang bermasalah. Coba beberapa saat lagi.';
  }

  return fallback;
}

