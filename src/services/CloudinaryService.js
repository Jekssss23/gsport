// Cloudinary Upload Service
// Using unsigned upload preset: gsc_reserve

const CLOUDINARY_CLOUD_NAME = 'dzlyiowkh';
const CLOUDINARY_UPLOAD_PRESET = 'gsc_reserve';

export class CloudinaryService {
  static async uploadImage(imageUri, options = {}) {
    try {
      console.log('Uploading to Cloudinary...');
      
      const timestamp = Date.now();
      // Backward compatible: some callers pass a string (prefix) as 2nd arg
      const normalizedOptions =
        typeof options === 'string' ? { filenamePrefix: options } : (options || {});

      const {
        folder = 'gsc/payment-proofs',
        filenamePrefix = 'upload',
      } = normalizedOptions;
      
      const formData = new FormData();
      formData.append('file', {
        uri: imageUri,
        type: 'image/jpeg',
        name: `${filenamePrefix}-${timestamp}.jpg`,
      });
      formData.append('upload_preset', CLOUDINARY_UPLOAD_PRESET);
      formData.append('folder', folder);

      const response = await fetch(
        `https://api.cloudinary.com/v1_1/${CLOUDINARY_CLOUD_NAME}/image/upload`,
        {
          method: 'POST',
          body: formData,
          headers: {
            Accept: 'application/json',
          },
        }
      );

      // Cloudinary should return JSON, but in some environments we might get HTML
      // (e.g. network captive portal, proxy error page, WAF). Handle safely.
      const rawText = await response.text();
      let data = null;
      try {
        data = rawText ? JSON.parse(rawText) : null;
      } catch (e) {
        const snippet = rawText ? rawText.slice(0, 250) : '';
        throw new Error(
          `Cloudinary response was not JSON (HTTP ${response.status}). ` +
          `First chars: ${snippet}`
        );
      }
      
      if (!response.ok) {
        console.error('Cloudinary error:', data);
        throw new Error(data.error?.message || 'Upload failed');
      }

      console.log('Cloudinary upload success:', data.secure_url);
      return data.secure_url;
    } catch (error) {
      console.error('Cloudinary upload error:', error);
      throw error;
    }
  }
}
