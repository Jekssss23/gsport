// Cloudinary Upload Service
// Using unsigned upload preset: gsc_reserve

const CLOUDINARY_CLOUD_NAME = 'dzlyiowkh';
const CLOUDINARY_UPLOAD_PRESET = 'gsc_reserve';

export class CloudinaryService {
  static async uploadImage(imageUri, userId) {
    try {
      console.log('Uploading to Cloudinary...');
      
      const timestamp = Date.now();
      
      const formData = new FormData();
      formData.append('file', {
        uri: imageUri,
        type: 'image/jpeg',
        name: `payment-${timestamp}.jpg`,
      });
      formData.append('upload_preset', CLOUDINARY_UPLOAD_PRESET);
      formData.append('folder', 'gsc/payment-proofs');

      const response = await fetch(
        `https://api.cloudinary.com/v1_1/${CLOUDINARY_CLOUD_NAME}/image/upload`,
        {
          method: 'POST',
          body: formData,
        }
      );

      const data = await response.json();
      
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
